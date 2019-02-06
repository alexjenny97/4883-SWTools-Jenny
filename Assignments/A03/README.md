This program srapes 2 NFL websites (using beutiful scraper), http://www.nfl.com/schedules/ and http://www.nfl.com/liveupdate/game-center/ , then puts data into json files. This program only scrapes data, it will be followed by a program that will compute statistics on the collected data. 

- imports: BeautifulScrapper(scrapes data), checkers (validates url being scraped), time (stops IP address from being dropped) 

- Scraping Ids
  - First the program scrapes all game ids from http://www.nfl.com/schedules/ and stores them in a list(called gameIds). This is done for all regular and post games from 2009 until now. This is done in two steps, first the regular game ids are obtained then the post game ids since the urls are different for each case. The program also has a sleep timer to avoid having the IP address dropped by the website due to over access. 

- Scraping game data
  - The program now scrapes  http://www.nfl.com/liveupdate/game-center/ to get actual game data (stats, player names, team names, ect.) and puts the dictionary into a folder of json files named with the gameid, NFLDataGameid.json. 
