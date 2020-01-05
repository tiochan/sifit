find . -type f -print0 | xargs  -0 -r -I file chmod 644 file
find . -type d -print0 | xargs  -0 -r -I dir chmod 755 dir

