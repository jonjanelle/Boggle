# Boggle Board Generator and Solver
# DWA-A2
This application draws a randomly-shuffled Boggle-style board using php.
Game pieces are modeled as 6-sided objects with one letter on each face. 

Data about cube configurations used in this program came from:
https://boardgamegeek.com/thread/300883/letter-distribution

Searches are performed using a recursive depth-first approach. This unfortunately 
is too inefficient an approach for solving the board (finding all possible words), 
but works well enough for single searches and for finding lists of small words. 

The English word list used is from: http://www.gwicks.net/dictionaries.htm
