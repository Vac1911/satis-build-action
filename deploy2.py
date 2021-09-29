import sys
import os
import re
import json
import shutil
import subprocess
import tarfile
from datetime import datetime

# This is a backport to python2 because none of our servers are updated

# Method to parse a datetime from in iso format
def parseDate(s):
    return datetime.strptime(s.replace('Z', ''), "%Y-%m-%dT%H:%M:%S")

# Get latest release data
result = subprocess.Popen("gh api /repos/QISCT/composer-monorepo/releases/latest", shell=True, stdout=subprocess.PIPE)
release = json.loads(result.stdout.read())

# Find the tarball in release assets
assets = release['assets']
tarball = False
for asset in assets:
    if asset['content_type'] == 'application/x-gtar':
        tarball = asset
        break

if not tarball:
    sys.exit("No tarball found in latest release")

# Skip deployment if release is not newer than currentRelease
if os.path.exists("dist/release.json"):
    with open("dist/release.json", "r") as f:
        currentRelease = json.loads(f.read())
        if parseDate(currentRelease['published_at']) >= parseDate(release['published_at']):
            sys.exit("No new releases to deploy")

# Remove previous tarball and download the new tarball via gh cli authentication
if os.path.exists("dist.tar.gz"):
  os.remove("dist.tar.gz")

os.system("gh release download " + release['tag_name'] + " --pattern 'dist.tar.gz' --repo QISCT/composer-monorepo")

# Unpack new release (doesn't delete previous dist folder until new release is fully unpacked)
if os.path.isdir("dist"):
    if os.path.isdir("prev_dist"):
        shutil.rmtree("prev_dist")
    shutil.move("dist", "prev_dist")

os.system("tar -xvf dist.tar.gz")

if os.path.isdir("prev_dist"):
    shutil.rmtree("prev_dist")

# Write release information to release.json
file = open("dist/release.json", "w")
file.write(json.dumps(release, indent=4))
file.close()