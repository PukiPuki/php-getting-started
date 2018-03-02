#!/bin/bash
expect -c "
	spawn pgcli -h ec2-174-129-26-203.compute-1.amazonaws.com -d daq5hqoilto32t -U nnjquhqhcsmbzg -p 5432
	expect \"Password for nnjquhqhcsmbzg: \r\"
	send \"ee407a056d0aa6ed4587a1aabee57672261bb4bc55addf7d78c018ca4dc133ee\r\"
	interact
"
