<?php
putenv("INPUT_REPOSITORY_HOST=github.com");
putenv("INPUT_REPOSITORY_ORGANIZATION=QISCT");
putenv("INPUT_PACKAGE_LIST=" . json_encode(['symfony-orm']));
putenv("INPUT_USER_NAME=Vac1911");
putenv("INPUT_USER_EMAIL=andrew@quasars.com");
putenv("GITHUB_TOKEN=123");
include_once __DIR__ . '/build.php';