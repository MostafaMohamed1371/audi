function isMediaArticleDetailPage(pathname: string) {
  return /^\/media\/(news|newsletter|city-meetings)\/.+/.test(pathname);
}

export function isInternalHeroPage(pathname: string) {
  if (pathname === "/strategy/focus-areas" || isMediaArticleDetailPage(pathname)) {
    return false;
  }

  return (
    pathname.startsWith("/about") ||
    pathname.startsWith("/strategy") ||
    pathname.startsWith("/programs") ||
    pathname.startsWith("/media") ||
    pathname.startsWith("/resources")
  );
}
