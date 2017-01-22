#!/bin/sh
set -e
initial_dir=`pwd`
./setupTestDB.sh
cd ../../src/tests/
echo ""
echo "Running tests..."
echo ""
phpunit authTest.php
cd $initial_dir
./RevertToOriginalDB.sh
