<?php
putenv("INPUT_PACKAGE_DIRECTORY=packages");
putenv("INPUT_REPOSITORY_HOST=github.com");
putenv("INPUT_REPOSITORY_ORGANIZATION=QISCT");
putenv("INPUT_REPOSITORY_NAME=composer-monorepo");
putenv("INPUT_USER_EMAIL=andrew@quasars.com");
putenv("INPUT_PACKAGE_LIST=" . json_encode(['symfony-orm']));
putenv("GITHUB_TOKEN=123");
putenv("GITHUB_SHA=sha");
include_once __DIR__ . '/build.php';