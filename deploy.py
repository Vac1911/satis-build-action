import sys
import os
import re
import json
import shutil
import subprocess
from datetime import datetime

# Method to parse a datetime from in iso format (with support for zulu time)
def parseDate(s):
    return datetime.fromisoformat(s.replace('Z', '+00:00'))

# Get latest release data
result = subprocess.run("gh api /repos/QISCT/composer-monorepo/releases/latest", shell=True, capture_output=True, text=True, check=True)
release = json.loads(result.stdout)

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
        if parseDate(currentRelease['created_at']) >= parseDate(release['created_at']):
            sys.exit("No new releases to deploy")

# Remove previous tarball and download the new tarball via gh cli authentication
if os.path.exists("dist.tar.gz"):
  os.remove("dist.tar.gz")
result = subprocess.run("gh release download " + release['tag_name'] + " --pattern 'dist.tar.gz' --repo QISCT/composer-monorepo", shell=True, capture_output=True, text=True, check=True)

# Unpack new release (doesn't delete previous dist folder until new release is fully unpacked)
if os.path.isdir("dist"):
    if os.path.isdir("prev_dist"):
        shutil.rmtree("prev_dist")
    shutil.move("dist", "prev_dist")
shutil.unpack_archive("dist.tar.gz", "", "gztar")
if os.path.isdir("prev_dist"):
    shutil.rmtree("prev_dist")

# Write release information to release.json
with open("dist/release.json", "w") as f:
    f.write(json.dumps(release, indent=4))