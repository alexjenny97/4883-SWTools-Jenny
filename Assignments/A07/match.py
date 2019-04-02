# Alex Jenny
# this program will take in a fileName and a folderName and will search for the closest image in the
# folder that is not the fileName, then it will show both images

# import the necessary packages
import numpy as np
import cv2
import sys
import os

# will get the mean square error of the two images
# parameters: imageA, imageB : the images to be compared
# returns: the mean square error or a high number (10000) if an image has type None
# small numbers indicate similarity
def mse(imageA, imageB):
	# super cool try-catch
	try:
	# the 'Mean Squared Error' between the two images is the
	# sum of the squared difference between the two images;
	# NOTE: the two images must have the same dimension
		err = np.sum((imageA.astype("float") - imageB.astype("float")) ** 2)
		err /= float(imageA.shape[0] * imageA.shape[1])

		# handle if a None type was given
	except AttributeError:
		return 10000 

	# return the MSE, the lower the error, the more "similar"
	# the two images are
	return err

# will determine if two images are the same image, this is to ensure the image passed
# in does not have a closest match of itself
# parameters: imageA, imageB : two possibly identical images
# returns: a truth value, true if the images are the same
def sameImage(imageA, imageB):
	if mse(imageA, imageB) == 0.0:
		return True
	else:
		 return False

# begining of program
#handle arguments, filename and folderName
args = {}
for arg in sys.argv[1:]:
    k,v = arg.split('=')
    args[k] = v

#open image, greyscale and resize, save folder
# resize to ensure comparison works
folder = (args["folderName"])
image = cv2.imread(folder + '/' + args["fileName"]) #image is in folder
image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY) #grey scale
image = cv2.resize(image, (255,255))

# make list of images in folder
images = []
#convert all images in folder to grey scale
for i in os.listdir(folder):
	pic = cv2.imread(folder + '/' + i) #get image from folder
	pic = cv2.cvtColor(pic, cv2.COLOR_BGR2GRAY) #greyscale
	pic = cv2.resize(pic, (255,255))
	images.append(pic) #add to list

# find best match
best = images[0] #best so far
for i in images: #for each image in folder
	newMatch = mse(i,image) # get mse for current image to checking image
	bestMatch = mse(best,image) # get mse for best so far and checking image
	if bestMatch == 0.0: # then the best match is itself, so force the next image becomes the best match
		bestMatch = 100000

		#if newMatch is closer to perfect than the bestMatch and newMatch is not 
		# the checking image then we have a new bestMatch
	if (newMatch < bestMatch) and (newMatch > 0.0):
		best = i

#show images
cv2.imshow("original", image)
cv2.imshow("best match", best)
cv2.waitKey(0)