#!/bin/bash
search_count=`cat search_count.txt`
inc="$((search_count+1))"
echo "$inc" > search_count.txt
echo "Commiting to Search_$inc"
git add -A
git commit -m "Search_$inc"
git push heroku master
