import subprocess
import os
import sys
from PIL import Image

def render_mmd(mmd_path, png_path, scale=2, width=None, bg="white"):
    cmd = f'cmd /c npx @mermaid-js/mermaid-cli -i "{mmd_path}" -o "{png_path}" -s {scale} -b {bg}'
    if width:
        cmd += f' -w {width}'
    res = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if res.returncode != 0:
        print(f"ERROR rendering {mmd_path}:", res.stderr)
        return False
    if os.path.exists(png_path):
        img = Image.open(png_path)
        print(f"SUCCESS: {os.path.basename(png_path)} -> {img.size[0]}x{img.size[1]} (ratio: {img.size[0]/img.size[1]:.2f})")
        return True
    return False

if __name__ == "__main__":
    if len(sys.argv) > 1:
        target = sys.argv[1]
        mmd_file = target if target.endswith('.mmd') else target + '.mmd'
        png_file = os.path.splitext(mmd_file)[0] + '.png'
        render_mmd(mmd_file, png_file)
    else:
        render_mmd("Blueprint/erd/taksonomi_relasi_kardinalitas.mmd", "Blueprint/erd/test_taksonomi.png", scale=2)
