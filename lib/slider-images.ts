import fs from "fs";
import path from "path";

export function getSliderImages(): string[] {
  const sliderDir = path.join(process.cwd(), "public", "slider");

  if (!fs.existsSync(sliderDir)) {
    return [];
  }

  return fs
    .readdirSync(sliderDir)
    .filter((file) => /\.(png|jpe?g|webp|avif)$/i.test(file))
    .sort((a, b) => a.localeCompare(b, undefined, { numeric: true }))
    .map((file) => `/slider/${file}`);
}
