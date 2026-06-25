type Props = {
  brand: string;
  brandEn?: string;
  description: string;
  bullets?: string[];
  isRtl: boolean;
  accent?: "orange" | "blue";
};

export function MediaIntro({
  brand,
  brandEn,
  description,
  bullets,
  isRtl,
  accent = "blue",
}: Props) {
  const accentColor = accent === "orange" ? "text-[#e8751a]" : "text-primary";

  return (
    <div
      dir={isRtl ? "rtl" : "ltr"}
      className="mb-12 grid gap-8 lg:grid-cols-2 lg:items-center lg:gap-16"
    >
      <div className={`${isRtl ? "text-start" : "text-start"}`}>
        <div className={accentColor}>
          {accent === "orange" ? (
            <div className="flex items-end gap-2">
              <div className="flex items-end gap-1" aria-hidden>
                {[40, 56, 32, 48, 24].map((height, index) => (
                  <span
                    key={index}
                    className="w-2 rounded-sm bg-[#e8751a]"
                    style={{ height }}
                  />
                ))}
              </div>
              <div>
                <p className="text-3xl font-bold sm:text-4xl">{brand}</p>
                {brandEn ? (
                  <p className="mt-1 text-sm font-medium tracking-[0.2em] text-[#e8751a]/80">
                    {brandEn}
                  </p>
                ) : null}
              </div>
            </div>
          ) : (
            <p className="text-3xl font-bold sm:text-4xl">{brand}</p>
          )}
        </div>
      </div>

      <div className={`space-y-4 ${isRtl ? "text-start" : "text-start"}`}>
        <p className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
          {description}
        </p>

        {bullets?.length ? (
          <ul className="space-y-2 text-base font-medium text-secondary">
            {bullets.map((bullet) => (
              <li key={bullet} className="flex items-center gap-2">
                <span
                  className="size-1.5 shrink-0 rounded-full bg-primary"
                  aria-hidden
                />
                {bullet}
              </li>
            ))}
          </ul>
        ) : null}
      </div>
    </div>
  );
}
