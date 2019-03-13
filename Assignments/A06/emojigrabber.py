#Alex J
#Program will save emojis from a web page to a file

# gets images
from beautifulscraper import BeautifulScraper
import requests # saves images
import urllib #????

# Use beatiful soup to read the page
SCRAPER = BeautifulScraper()
url = 'https://www.webfx.com/tools/emoji-cheat-sheet/'
page = SCRAPER.go(url)

# then loop through the page with the following
for emoji in page.find_all("span",{"class":"emoji"}): #for each emoji
    image_path = emoji['data-src'] #get relative path to emoji
    emojiData = requests.get(url+image_path, stream = True) #get bytes of image
    nameArray = image_path.split('/') #puts relative path as an array
    with open("./emojis/"+nameArray[-1], 'wb') as f: # need alias so I don't keep rewriting the file
        for chunck in emojiData:
            f.write(chunck)