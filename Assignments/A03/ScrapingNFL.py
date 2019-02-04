from beautifulscraper import BeautifulScraper
from validator_collection import checkers
import os,sys
import json
import pprint as pp
import time
Data = {}

gameIds = []
scraper = BeautifulScraper()
# fix later to get all data
years = [x for x in range(2009, 2019)]
stype = "REG"
weeks = [x for x in range(1,18)]



# get game ids for regular!!
for yr in years:
    for wk in weeks:
        time.sleep(2)
        url = "http://www.nfl.com/schedules/%d/%s%s" % (yr,stype,str(wk))
        if(checkers.is_url(url)):
            page = scraper.go(url)
            divs = page.find_all("div", {"class" : "schedules-list-content"})

            for d in divs:
                gameIds.append(d["data-gameid"])



#get game ids for post!
stype = "POST"

for yr in years:
    time.sleep(.2)
    url = "http://www.nfl.com/schedules/%d/%s" % (yr,stype)
    if(checkers.is_url(url)):
        page = scraper.go(url)
        divs = page.find_all("div", {"class" : "schedules-list-content"})

        for d in divs:
            gameIds.append(d["data-gameid"])




#for all game ids
for g in gameIds:
    time.sleep(.2)
    url = "http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json" % (g,g)
    if(checkers.is_url(url)):
        page = scraper.go(url)
        json.dump(page.get_text(), open("NFLData/NFLData%s.json" % (g), 'w'))


