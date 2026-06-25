import { UrbanPoliciesPageShell } from "@/app/components/programs/urban-policies/page/shell";
import { setRequestLocale } from "next-intl/server";
import { Suspense } from "react";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function UrbanPoliciesPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <Suspense fallback={null}>
      <UrbanPoliciesPageShell />
    </Suspense>
  );
}
