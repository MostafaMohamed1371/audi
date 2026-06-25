"use client";

import { cn } from "@/lib/utils";

type ValueItem = {
  title: string;
  description: string;
};

type Props = {
  title: string;
  items: ValueItem[];
  isRtl: boolean;
};

export function InstituteValuesSection({ title, items, isRtl }: Props) {
  const titleCol = isRtl ? "col-start-1" : "col-start-3";
  const dotCol = "col-start-2";
  const descCol = isRtl ? "col-start-3" : "col-start-1";

  return (
    <section className="bg-white py-16 sm:py-20 lg:py-28">
      <div className="mx-auto max-w-5xl px-4 sm:px-6">
        <h2 className="mb-14 flex items-center justify-center gap-3 text-2xl font-bold text-secondary sm:mb-20 sm:text-3xl lg:text-4xl">
          <span className="size-2.5 shrink-0 rounded-sm bg-primary" aria-hidden />
          {title}
        </h2>

        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="relative mx-auto max-w-4xl"
        >
          <div
            className={cn(
              "grid gap-x-6 lg:gap-x-10",
              isRtl
                ? "grid-cols-[minmax(0,11rem)_2rem_minmax(0,1fr)]"
                : "grid-cols-[minmax(0,1fr)_2rem_minmax(0,11rem)]"
            )}
          >
            <div
              aria-hidden
              className="col-start-2 row-start-1 flex justify-center"
              style={{ gridRow: `1 / ${items.length + 1}` }}
            >
              <div className="h-full w-px bg-primary" />
            </div>

            {items.map((item, index) => {
              const number = String(index + 1).padStart(2, "0");
              const row = index + 1;

              return (
                <div key={number} className="contents">
                  <div
                    className={cn(
                      titleCol,
                      "flex items-center gap-3 py-8 sm:py-9 lg:py-10",
                      isRtl ? "justify-start" : "justify-end"
                    )}
                    style={{ gridRow: row }}
                  >
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-full border-2 border-primary/45 bg-white text-sm font-bold text-primary">
                      {number}
                    </span>
                    <h3
                      dir={isRtl ? "rtl" : "ltr"}
                      className="text-xl font-bold text-secondary sm:text-2xl"
                    >
                      {item.title}
                    </h3>
                  </div>

                  <div
                    className={cn(dotCol, "flex items-center justify-center py-8 sm:py-9 lg:py-10")}
                    style={{ gridRow: row }}
                  >
                    <span className="relative z-10 size-3 rounded-full bg-primary" />
                  </div>

                  <div
                    className={cn(descCol, "flex items-center py-8 sm:py-9 lg:py-10")}
                    style={{ gridRow: row }}
                  >
                    <p
                      dir={isRtl ? "rtl" : "ltr"}
                      className={cn(
                        "w-full text-sm leading-8 text-secondary sm:text-[0.95rem] sm:leading-9",
                        isRtl ? "text-start" : "text-end"
                      )}
                    >
                      {item.description}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
}
