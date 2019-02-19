#!/bin/sh
RET=0
for i in $(find . | grep .php); do
	php -l $i > /dev/null
	[ $? = 1 ] && RET=1
done

[ $RET = 1 ] && echo "Some errors"
[ $RET = 0 ] && echo "Ok"
exit $RET

