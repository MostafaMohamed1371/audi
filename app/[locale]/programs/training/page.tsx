import { TrainingPageShell } from "@/app/components/programs/training/page/shell";
import { setRequestLocale } from "next-intl/server";
import { Suspense } from "react";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function TrainingPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <Suspense fallback={null}>
      <TrainingPageShell />
    </Suspense>
  );
}
