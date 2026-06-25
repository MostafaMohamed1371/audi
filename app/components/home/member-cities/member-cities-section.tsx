import { MemberCitiesMap } from "@/app/components/home/member-cities/member-cities-map";
import { fetchMemberCityStats, type MemberCityStat } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

type StatItem = {
  key?: string;
  label: string;
  value: string;
  unit: string;
};

type Props = {
  title?: string;
  stats?: MemberCityStat[];
};

export async function MemberCitiesSection({ title, stats: statsProp }: Props = {}) {
  const t = await getTranslations("home.memberCities");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiStats = statsProp ?? (await fetchMemberCityStats(locale));
  const fallbackStats = t.raw("stats") as StatItem[];
  const stats: StatItem[] = apiStats
    ? apiStats.map((stat) => ({
        key: stat.key,
        label: stat.label,
        value: String(stat.value),
        unit: stat.unit,
      }))
    : fallbackStats;

  return (
    <section
      id="member-cities"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <h2 className="mb-8 text-center text-2xl font-bold text-secondary sm:mb-12 sm:text-3xl lg:text-4xl">
          {title ?? t("title")}
        </h2>

        <div className="mb-6 overflow-hidden rounded-2xl border border-border/60 bg-white shadow-[0_8px_32px_rgba(17,31,66,0.06)] sm:mb-8">
          <div className="grid divide-y divide-border/70 sm:grid-cols-3 sm:divide-x sm:divide-y-0 rtl:sm:divide-x-reverse">
            {stats.map((stat) => (
              <div
                key={stat.key ?? stat.label}
                className="flex items-center justify-center gap-3 px-4 py-5 sm:px-6 sm:py-8"
              >
                <div className="text-center">
                  <p className="text-xs font-medium text-muted-foreground sm:text-sm">
                    {stat.label}
                  </p>
                  <p className="mt-1 flex flex-wrap items-baseline justify-center gap-1.5 sm:gap-2">
                    <span className="text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
                      {stat.value}
                    </span>
                    <span className="text-base font-medium text-muted-foreground">
                      {stat.unit}
                    </span>
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>

        <MemberCitiesMap isRtl={isRtl} />
      </div>
    </section>
  );
}
