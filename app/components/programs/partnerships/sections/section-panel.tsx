import Image from "next/image";
import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import type {
  PartnershipSectionContent,
  PartnershipsPanelProps,
} from "@/app/components/programs/partnerships/shared/types";
import { cn } from "@/lib/utils";

type Props = PartnershipsPanelProps & {
  content: PartnershipSectionContent;
};

export function PartnershipSectionPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  return (
    <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
      <div
        dir={isRtl ? "rtl" : "ltr"}
        className="grid items-center gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16"
      >
        <div className="relative mx-auto w-full max-w-[540px] lg:col-span-5 lg:mx-0">
          <div
            className={cn(
              "absolute -bottom-5 h-[88%] w-[58%] rounded-2xl bg-secondary",
              isRtl ? "-start-5" : "-end-5",
            )}
            aria-hidden
          />
          <Image
            src="/partnerships/image-accent.png"
            alt=""
            width={120}
            height={120}
            className={cn(
              "pointer-events-none absolute z-0 w-[28%] max-w-[120px] opacity-90",
              isRtl ? "-bottom-3 -start-3" : "-bottom-3 -end-3",
            )}
            aria-hidden
          />
          <div className="relative z-10 overflow-hidden rounded-[10px] shadow-[1px_1px_18px_0px_#111F4214]">
            {content.image.endsWith(".gif") ? (
              // eslint-disable-next-line @next/next/no-img-element
              <img
                src={content.image}
                alt={content.title}
                className="h-auto w-full object-cover"
              />
            ) : (
              <Image
                src={content.image}
                alt={content.title}
                width={540}
                height={400}
                className="h-auto w-full object-cover"
                sizes="(max-width: 1024px) 100vw, 540px"
                priority
              />
            )}
          </div>
        </div>

        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="space-y-6 text-start lg:col-span-7"
        >
          <h2 className="text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
            {content.title}
          </h2>
          <p className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
            {content.intro}
          </p>
        </div>
      </div>
    </PanelWrapper>
  );
}
