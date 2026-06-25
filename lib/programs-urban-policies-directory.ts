export type PortalDirectoryTab =
  | "cities"
  | "projects"
  | "organizations"
  | "publications";

export const PORTAL_DIRECTORY_TABS: PortalDirectoryTab[] = [
  "cities",
  "projects",
  "organizations",
  "publications",
];

export function isPortalDirectoryTab(
  value: string,
): value is PortalDirectoryTab {
  return PORTAL_DIRECTORY_TABS.includes(value as PortalDirectoryTab);
}
