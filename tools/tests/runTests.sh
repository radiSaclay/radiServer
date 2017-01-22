#!/bin/sh
set -e
initial_dir=`pwd`
./setupTestDB.sh
cd ../../
php tools/database-reboot.php
cd $initial_dir
cd ../../src/tests/
echo ""
echo "Running tests..."
echo ""
phpunit .
cd $initial_dir
./RevertToOriginalDB.sh
