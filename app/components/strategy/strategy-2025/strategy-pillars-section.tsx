import { cn } from "@/lib/utils";

type Pillar = {
  number: string;
  text: string;
};

type Props = {
  pillars: Pillar[];
  isRtl: boolean;
};

export function StrategyPillarsSection({ pillars, isRtl }: Props) {
  return (
    <section dir={isRtl ? "rtl" : "ltr"} className="bg-white pb-16 sm:pb-20 lg:pb-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="overflow-hidden rounded-[30px] bg-primary lg:grid lg:grid-cols-3 py-5 px-6">
          {pillars.map((pillar, index) => (
            <article
              key={pillar.number}
              tabIndex={0}
              className={cn(
                "group relative flex min-h-[220px] items-center justify-center px-6 py-10 sm:min-h-[240px] sm:px-8 sm:py-12",
                index > 0 && "border-t border-white/15 lg:border-t-0 lg:border-s lg:border-white/15",
              )}
            >
              <div
                aria-hidden
                className={cn(
                  "pointer-events-none absolute inset-5 rounded-[30px] border border-white bg-transparent opacity-0 transition-all duration-300 sm:inset-6",
                  "group-hover:bg-white/10 group-hover:opacity-100 group-focus-within:bg-white/10 group-focus-within:opacity-100",
                )}
              />

              <span
                className={cn(
                  "pointer-events-none absolute top-5 z-10 -translate-y-1/2 rounded-lg border border-white bg-primary px-4 py-2 text-lg font-bold leading-none text-white opacity-0 transition-opacity duration-300 sm:top-6 sm:px-5 sm:py-2.5 sm:text-xl",
                  "group-hover:opacity-100 group-focus-within:opacity-100",
                  isRtl ? "end-8 sm:end-10" : "start-8 sm:start-10",
                )}
              >
                {pillar.number}
              </span>

              <p className="relative z-0 max-w-xs text-start text-sm leading-8 text-white sm:max-w-sm sm:text-[0.95rem] sm:leading-8">
                {pillar.text}
              </p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
