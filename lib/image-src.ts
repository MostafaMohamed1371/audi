/**
 * Resolve an image URL from the API or legacy translation JSON.
 * API values are root-relative (/blog/1.png) or absolute (http…).
 * Legacy fallbacks may be bare filenames and need a directory prefix.
 */
export function resolveImageSrc(
  url: string | null | undefined,
  legacyPrefix?: string,
): string {
  if (!url) {
    return "";
  }

  if (
    url.startsWith("/") ||
    url.startsWith("http://") ||
    url.startsWith("https://")
  ) {
    return url;
  }

  if (legacyPrefix) {
    return `${legacyPrefix.replace(/\/$/, "")}/${url}`;
  }

  return `/${url}`;
}

export function isRemoteImage(src: string): boolean {
  return src.startsWith("http://") || src.startsWith("https://");
}
