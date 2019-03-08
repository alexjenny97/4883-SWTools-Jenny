import os
import sys
from PIL import Image, ImageDraw, ImageFont, ImageFilter


def img_to_ascii(**kwargs):
    """ 
    The ascii character set we use to replace pixels. 
    The grayscale pixel values are 0-255.
    0 - 25 = '#' (darkest character)
    250-255 = '.' (lightest character)
    """
    ascii_chars = [ u'Z', u'Q', u'T', u'W', u'E', u'K', u'P', u'L', u'I', u'C', u'Y']
  
    width = kwargs.get('width',200)
    path = kwargs.get('path',None)



    im = Image.open(path)

    im = resize(im,width)

    # w,h = im.size

    # this is used as storage. It stores the original picture's color values
    objToGo = list(im.convert("RGBA").getdata())

    im = im.convert("L") # convert to grayscale

    imlist = list(im.getdata())

    i = 0
    j = 0
    # chList is the characters that will be printed. It is a 2D array
    chList = []
    chList.append([])
    for val in imlist:
        ch = ascii_chars[val // 25] #.decode('utf-8')
        chList[j].append(ch)
        sys.stdout.write(ch)
        i += 1
        if i % width == 0:
            sys.stdout.write("\n")
            chList.append([])
            j += 1
            i = 0

    return chList,objToGo

    

def resize(img,width):
    """
    This resizes the img while maintining aspect ratio. Keep in 
    mind that not all images scale to ascii perfectly because of the
    large discrepancy between line height line width (characters are 
    closer together horizontally then vertically)
    """
    
    wpercent = float(width / float(img.size[0]))
    hsize = int((float(img.size[1])*float(wpercent)))
    img = img.resize((width ,hsize), Image.ANTIALIAS)

    return img




if __name__=='__main__':
    # picture to print
    path = str(sys.argv[1])
    #cahracters and color values
    Ascii,colors = img_to_ascii(path=path,width=800)

    hsize = 1200
    width = 800
    # Open a new image using 'RGBA' (a colored image with alpha channel for transparency)
    #              color_type      (w,h)     (r,g,b,a) 
    #                   \           /            /
    #                    \         /            /
    newImg = Image.new('RGBA', (800, 1200), (255,255,255,255))

    # Open a TTF file and specify the font size.
    fnt = ImageFont.truetype(str(sys.argv[3]), int(sys.argv[4]))

    # get a drawing context for your new image
    drawOnMe = ImageDraw.Draw(newImg)


    #Loop through picture and print the character in that spot in array with the color 
    # value of the original spot    
    for h in range(hsize):
        for w in range(width):
            # since chars are bigger then pixels, we want to space our chars out
           if h % 10 == 0:
               if w % 10 == 0:
                    # add a character to some xy 
                    #         location   character  ttf-font   color-tuple
                    #            \         /        /            /
                    #             \       /        /            /
                   drawOnMe.text((w,h), Ascii[h][w], font=fnt, fill=(colors[h*width + w]))

    
    # Display your new image with all the stuff `drawOnMe` placed on it
    newImg.show()

    # Save the image.
    newImg.save(str(sys.argv[2]))
