import { InnerPageShell } from "@/app/components/layout/inner-page-shell";
import { Link } from "@/i18n/routing";
import { fetchJobOpenings } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";
import { MapPin } from "lucide-react";

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

export async function CareersContent() {
  const t = await getTranslations("careers");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const openings = await fetchJobOpenings(locale);

  return (
    <InnerPageShell title={t("pages.title")} subtitle={t("pages.subtitle")}>
      {openings.length === 0 ? (
        <p className="text-center text-muted-foreground">{t("empty")}</p>
      ) : (
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="grid gap-6 sm:grid-cols-2 lg:gap-8"
        >
          {openings.map((job) => (
            <article
              key={job.id}
              className="flex h-full flex-col rounded-2xl border border-border/60 bg-white p-6 shadow-[1px_1px_18px_0px_#111F4214] sm:p-8"
            >
              <div className="mb-4 flex flex-wrap items-center gap-2">
                <span className="rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
                  {employmentTypeLabel(t, job.employmentType)}
                </span>
              </div>

              <h2 className="mb-3 text-xl font-bold text-secondary">{job.title}</h2>

              {job.summary ? (
                <p className="mb-4 flex-1 text-sm leading-7 text-muted-foreground sm:text-base sm:leading-8">
                  {job.summary}
                </p>
              ) : null}

              {job.location ? (
                <p className="mb-6 flex items-center gap-2 text-sm text-muted-foreground">
                  <MapPin className="size-4 shrink-0 text-primary" aria-hidden />
                  {job.location}
                </p>
              ) : null}

              <Link
                href={{ pathname: "/careers/[id]", params: { id: String(job.id) } }}
                className="inline-flex w-fit rounded-full bg-primary px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary/90"
              >
                {t("viewDetails")}
              </Link>
            </article>
          ))}
        </div>
      )}
    </InnerPageShell>
  );
}
