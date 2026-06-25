export type PartnershipsTab =
  | "euroArabDialogue"
  | "secretarySpeaks"
  | "urbanAwards"
  | "partnersGuide";

export const PARTNERSHIPS_TABS: PartnershipsTab[] = [
  "euroArabDialogue",
  "secretarySpeaks",
  "urbanAwards",
  "partnersGuide",
];

export function isPartnershipsTab(value: string): value is PartnershipsTab {
  return PARTNERSHIPS_TABS.includes(value as PartnershipsTab);
}
