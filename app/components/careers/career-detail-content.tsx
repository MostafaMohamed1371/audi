import { JobApplicationForm } from "@/app/components/careers/job-application-form";
import { InnerPageShell } from "@/app/components/layout/inner-page-shell";
import { Link } from "@/i18n/routing";
import { fetchJobOpening } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";
import { ArrowRight, MapPin } from "lucide-react";
import { notFound } from "next/navigation";

const EMPLOYMENT_TYPES = [
  "full_time",
  "part_time",
  "contract",
  "internship",
] as const;

function employmentTypeLabel(
  t: Awaited<ReturnType<typeof getTranslations<"careers">>>,
  type: string,
) {
  if (EMPLOYMENT_TYPES.includes(type as (typeof EMPLOYMENT_TYPES)[number])) {
    return t(`employmentTypes.${type as (typeof EMPLOYMENT_TYPES)[number]}`);
  }

  return type;
}

type Props = {
  id: string;
};

export async function CareerDetailContent({ id }: Props) {
  const t = await getTranslations("careers");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const job = await fetchJobOpening(id, locale);
  if (!job) {
    notFound();
  }

  return (
    <InnerPageShell title={job.title}>
      <article dir={isRtl ? "rtl" : "ltr"} className="mx-auto max-w-3xl">
        <Link
          href="/careers"
          className="mb-8 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
        >
          <ArrowRight className="size-4 rtl:rotate-180" aria-hidden />
          {t("backToList")}
        </Link>

        <div className="mb-6 flex flex-wrap gap-4 text-sm text-muted-foreground">
          {job.location ? (
            <span className="inline-flex items-center gap-2">
              <MapPin className="size-4 text-primary" aria-hidden />
              <strong className="font-medium text-secondary">
                {t("detail.location")}:
              </strong>{" "}
              {job.location}
            </span>
          ) : null}
          <span>
            <strong className="font-medium text-secondary">
              {t("detail.employmentType")}:
            </strong>{" "}
            {employmentTypeLabel(t, job.employmentType)}
          </span>
        </div>

        {job.summary ? (
          <p className="mb-8 text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
            {job.summary}
          </p>
        ) : null}

        {job.description.length > 0 ? (
          <div className="space-y-4">
            <h2 className="text-lg font-bold text-secondary">
              {t("detail.responsibilities")}
            </h2>
            <ul className="list-disc space-y-2 ps-5 text-base leading-8 text-muted-foreground">
              {job.description.map((item) => (
                <li key={item.slice(0, 40)}>{item}</li>
              ))}
            </ul>
          </div>
        ) : null}

        <JobApplicationForm jobOpeningId={job.id} isRtl={isRtl} />
      </article>
    </InnerPageShell>
  );
}
