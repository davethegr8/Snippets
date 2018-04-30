# Finds files bigger than 10M and displays size
find . -not -iwholename '*.git*' -type f -size +10M | xargs -I {} du -sh {} 

# run something with ruby
docker run --rm -v `pwd`:/usr/src/ -w /usr/src ruby:2.5
