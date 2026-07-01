import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import type {
  ConsultingContent,
  TrainingPanelProps,
} from "@/app/components/programs/training/shared/types";
import { cn } from "@/lib/utils";
import Image from "next/image";

type Props = TrainingPanelProps & {
  content: ConsultingContent;
};

export function ConsultingPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  const heroImage = content.image ?? "/projects/p2.png";
  const detailImage = content.detailImage ?? "/projects/consulting-presenter.png";

  return (
    <div className="space-y-0">
      <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
        <div dir={isRtl ? "rtl" : "ltr"} className="space-y-8 lg:space-y-10">
          <div className="space-y-4 text-start">
            <h2 className="text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
              {content.title}
            </h2>
            <p className="max-w-4xl text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
              {content.intro}
            </p>
          </div>

          <div
            className="relative isolate grid lg:grid-cols-12 lg:items-center lg:gap-0"
            dir={isRtl ? "rtl" : "ltr"}
          >
            <div
              className={cn(
                "h-[394px] w-full max-w-[955px] overflow-hidden rounded-[10px] bg-[#eef2f6] lg:col-span-7 lg:row-start-1 lg:z-10",
                isRtl ? "lg:col-start-6 lg:justify-self-end" : "lg:col-start-1 lg:justify-self-start",
              )}
            >
              <Image
                src={heroImage}
                alt=""
                width={955}
                height={394}
                className="h-full w-full object-cover"
                sizes="(max-width: 1024px) 100vw, 955px"
              />
            </div>

            <div
              className={cn(
                "space-y-3 lg:col-span-5 lg:row-start-1 lg:z-0 lg:-me-16",
                isRtl ? "lg:col-start-1" : "lg:col-start-8",
              )}
            >
              {content.nav.map((item) => (
                <div
                  key={item}
                  dir={isRtl ? "rtl" : "ltr"}
                  className={cn(
                    "flex items-center gap-3 border border-primary/20 bg-[#eef4f8] px-5 py-4 shadow-sm",
                    isRtl ? "rounded-s-2xl rounded-e-lg" : "rounded-e-2xl rounded-s-lg",
                  )}
                >
                  <span
                    className="size-2.5 shrink-0 rounded-full bg-primary"
                    aria-hidden
                  />
                  <span className="flex-1 text-start text-sm font-semibold leading-snug text-secondary sm:text-base">
                    {item}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </PanelWrapper>

      <section className="mt-14 border-y border-primary/25 bg-[#eef4f8] px-4 py-14 sm:mt-16 sm:px-6 sm:py-16 lg:py-20">
        <div className="mx-auto max-w-7xl">
          <div
            className="grid items-start gap-10 lg:grid-cols-12 lg:gap-x-16 lg:gap-y-12"
            dir={isRtl ? "rtl" : "ltr"}
          >
            <div className="mx-auto w-full max-w-[380px] overflow-hidden rounded-[24px] bg-white shadow-[1px_1px_18px_0px_#111F4214] lg:col-span-5 lg:mx-0 lg:row-span-3">
              <Image
                src={detailImage}
                alt=""
                width={380}
                height={520}
                className="h-auto w-full object-cover object-top"
                sizes="380px"
              />
            </div>

            <div className="space-y-12 lg:col-span-7">
              {content.sections.map((section, index) => (
                <article
                  key={section.title}
                  className={cn(
                    "text-start",
                    index === 1 &&
                      "lg:flex lg:items-stretch lg:justify-between lg:gap-10",
                  )}
                >
                  <div className="space-y-4">
                    <h3 className="text-xl font-bold text-secondary sm:text-2xl">
                      {section.title}
                    </h3>
                    <p className="text-base leading-8 text-muted-foreground sm:leading-9">
                      {section.description}
                    </p>
                  </div>
                  {index === 1 ? (
                    <span
                      className="mx-auto mt-6 hidden w-3 shrink-0 rounded-full bg-primary lg:mx-0 lg:mt-0 lg:block lg:min-h-[140px] lg:self-center"
                      aria-hidden
                    />
                  ) : null}
                </article>
              ))}
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
