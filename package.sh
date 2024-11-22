#!/bin/bash

# Plugin packaging script for Simple Top Banner
# Usage: ./package.sh [version]

# Check if version argument is provided
if [ -z "$1" ]; then
    echo "Please provide a version number."
    echo "Usage: ./package.sh 1.1.0"
    exit 1
fi

VERSION=$1
PLUGIN_SLUG="simple-top-banner"
TEMP_DIR="/tmp/$PLUGIN_SLUG"
PACKAGE_NAME="$PLUGIN_SLUG-$VERSION"

echo "📦 Packaging $PACKAGE_NAME..."

# Create temp directory
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

# Copy files to temp directory
echo "📁 Copying files..."
cp -r assets index.php readme.txt top-banner.php "$TEMP_DIR/"

# Clean up any development or system files
echo "🧹 Cleaning up..."
find "$TEMP_DIR" -name '.DS_Store' -type f -delete
find "$TEMP_DIR" -name '.git*' -type f -delete
find "$TEMP_DIR" -name '*.map' -type f -delete
find "$TEMP_DIR" -name 'package.sh' -type f -delete

# Create zip file
echo "🤐 Creating zip archive..."
cd /tmp
zip -r "$PACKAGE_NAME.zip" "$PLUGIN_SLUG" -x "*.git*" "*.DS_Store"

# Move zip to original directory
mv "$PACKAGE_NAME.zip" "$OLDPWD/releases/"

# Clean up
rm -rf "$TEMP_DIR"

echo "✅ Package created: $PACKAGE_NAME.zip"
echo "📊 Package contents:"
unzip -l "$OLDPWD/releases/$PACKAGE_NAME.zip"
