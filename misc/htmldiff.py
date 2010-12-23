#!/usr/bin/python
import sys, os
from difflib import HtmlDiff

def main():
	if len(sys.argv) != 3:
		print "%s <oldfile> <newfile>" % sys.argv[0]
		print
		sys.exit(1)
	
	oldfile, newfile = sys.argv[1:]

	if not os.path.exists(oldfile) or not os.path.exists(newfile):
		print "At least one of the files does not exist."
		sys.exit(2)

	differ = HtmlDiff(4, 70)

	with open(oldfile, "r") as fh:
		fromlines = [line for line in fh]

	with open(newfile, "r") as fh:
		tolines = [line for line in fh]

	with open("diff.html", "w") as fh:
		fh.write(differ.make_file(fromlines, tolines))


if __name__ == '__main__':
	main()
