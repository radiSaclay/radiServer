#!/bin/sh
set -e
echo ""
echo "Reverting back to original database..."
echo ""
initial_dir=`pwd`
propel_dir=../../propel
propel_bin_from_propel_dir=../vendor/bin/propel

cd $propel_dir
# Generate connection file (linking to the TestDB)
$propel_bin_from_propel_dir config:convert
# Rebuild Classes, not really necessary but nice to make sure its all working as intended and prevent conflicts with older autogenerated files
$propel_bin_from_propel_dir sql:build  --overwrite
$propel_bin_from_propel_dir model:build
echo ""
echo "You are using in the original database"
echo ""
