#!/usr/bin/env bash
# Losslessly re-encode / compress raster assets in public/assets (images + registration MP3).
# Run from repo root: bash scripts/optimize-assets.sh
# Requires: ffmpeg (audio), ImageMagick magick or convert (images). Install on Ubuntu: apt install ffmpeg imagemagick
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
BACKUP="$ROOT/var/asset-optimize-backup"
mkdir -p "$BACKUP"

MAGICK=""
if command -v magick >/dev/null 2>&1; then MAGICK=magick
elif command -v convert >/dev/null 2>&1; then MAGICK=convert
fi

optimize_jpeg() {
  local f="$1"
  [[ -f "$f" ]] || return 0
  cp -a "$f" "$BACKUP/$(echo "$f" | tr '/' '_')"
  $MAGICK "$f" -strip -interlace Plane -sampling-factor 4:2:0 -quality 82 "$f.tmp" && mv "$f.tmp" "$f"
  echo "OK jpeg: $f"
}

optimize_png() {
  local f="$1"
  [[ -f "$f" ]] || return 0
  cp -a "$f" "$BACKUP/$(echo "$f" | tr '/' '_')"
  $MAGICK "$f" -strip "$f.tmp" && mv "$f.tmp" "$f"
  echo "OK png: $f"
}

compress_mp3() {
  local f="$1"
  [[ -f "$f" ]] || return 0
  cp -a "$f" "$BACKUP/$(basename "$f")"
  ffmpeg -y -hide_banner -loglevel error -i "$f" -codec:a libmp3lame -b:a 128k "$f.tmp" && mv "$f.tmp" "$f"
  echo "OK mp3: $f"
}

if command -v ffmpeg >/dev/null 2>&1; then
  compress_mp3 "public/assets/audio/audio-4.mp3" || true
else
  echo "ffmpeg not found; skipping audio." >&2
fi

if [[ -n "$MAGICK" ]]; then
  while IFS= read -r -d '' f; do
    optimize_jpeg "$f" || true
  done < <(find public/assets/images -type f \( -iname '*.jpg' -o -iname '*.jpeg' \) -print0 2>/dev/null)

  while IFS= read -r -d '' f; do
    optimize_png "$f" || true
  done < <(find public/assets/images -type f -iname '*.png' -print0 2>/dev/null)
else
  echo "ImageMagick not found; skipping images." >&2
fi

echo "Backups under $BACKUP (if any step ran)."
