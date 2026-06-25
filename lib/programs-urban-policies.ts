export type UrbanPoliciesTab =
  | "developmentPortal"
  | "developmentIndex"
  | "innovationLab"
  | "practiceReports";

export const URBAN_POLICIES_TABS: UrbanPoliciesTab[] = [
  "developmentPortal",
  "developmentIndex",
  "innovationLab",
  "practiceReports",
];

export function isUrbanPoliciesTab(value: string): value is UrbanPoliciesTab {
  return URBAN_POLICIES_TABS.includes(value as UrbanPoliciesTab);
}
