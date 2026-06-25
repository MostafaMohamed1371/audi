import { PartnershipsPageShell } from "@/app/components/programs/partnerships/page/shell";
import { setRequestLocale } from "next-intl/server";
import { Suspense } from "react";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function PartnershipsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <Suspense fallback={null}>
      <PartnershipsPageShell />
    </Suspense>
  );
}
