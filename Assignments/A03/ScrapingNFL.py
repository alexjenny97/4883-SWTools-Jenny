# Alex Jenny
# Scrapes NFL websites to get json files with all stats
import json
import time
from beautifulscraper import BeautifulScraper
from validator_collection import checkers


DATA = {}
GAME_IDS = []
SCRAPER = BeautifulScraper()
YEARS = [x for x in range(2009, 2019)] # 2009 - 2018
S_TYPE = "REG"
WEEKS = [x for x in range(1, 18)] # 17 REG games per year



# get game ids for regular!!
for yr in YEARS:
    for wk in WEEKS:
        time.sleep(.2) # good practice to not be too fast with requests
        # Since post games are handeled S_TYPE could be hard-coded but this also works 
        url = "http://www.nfl.com/schedules/%d/%s%s" % (yr, S_TYPE, str(wk))
        if checkers.is_url(url): # then url has data
            page = SCRAPER.go(url) 
            divs = page.find_all("div", {"class" : "schedules-list-content"}) # div contains gameid

            for d in divs:
                GAME_IDS.append(d["data-gameid"]) #all regular game ids are being  stored



#get game ids for post!
S_TYPE = "POST"

for yr in YEARS: # now week number for post season games
    time.sleep(.2)
    url = "http://www.nfl.com/schedules/%d/%s" % (yr, S_TYPE) 
    if checkers.is_url(url):
        page = SCRAPER.go(url)
        divs = page.find_all("div", {"class" : "schedules-list-content"})

        for d in divs:
            GAME_IDS.append(d["data-gameid"])


# get actual data for all game ids
for g in GAME_IDS:
    time.sleep(.2)
    # this url requires a game id
    url = "http://www.nfl.com/liveupdate/game-center/%s/%s_gtd.json" % (g,g)
    if checkers.is_url(url):
        page = SCRAPER.go(url)
        # put data into a json file. about 2500
        json.dump(page.get_text(), open("NFLData/NFLData%s.json" % (g), 'w'))
        