import Image from "next/image";
import { CourseRow } from "@/app/components/programs/training/sections/training-programs/course-row";
import { FormatBox } from "@/app/components/programs/training/shared/format-box";
import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import type {
  TrainingPanelProps,
  TrainingProgramsContent,
} from "@/app/components/programs/training/shared/types";

type Props = TrainingPanelProps & {
  content: TrainingProgramsContent;
};

export function TrainingProgramsPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  return (
    <div className="space-y-0">
      <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="grid items-center gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16"
        >
          <div className="mx-auto w-full max-w-[614px] lg:mx-0 lg:justify-self-end   space-y-8 lg:col-span-5">
            <div className="aspect-[614/538] overflow-hidden rounded-2xl border border-primary/30 bg-white">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src="/icons/program/6.gif"
                alt=""
                className="size-full object-cover"
              />
            </div>
          </div>
          <div dir={isRtl ? "rtl" : "ltr"} className="space-y-8 lg:col-span-5  lg:col-span-7">
            <h2 className="text-start text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
              {content.title}
            </h2>
            <p className="text-start text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
              {content.intro}
            </p>
            <h3 className="text-start text-xl font-bold text-secondary sm:text-2xl">
              {content.formatsTitle}
            </h3>
            <div className="space-y-3">
              {content.formats.map((format) => (
                <FormatBox key={format} text={format} isRtl={isRtl} />
              ))}
            </div>
          </div>

        </div>
      </PanelWrapper>

      <section className="mt-14 bg-[#eef4f8] px-4 py-14 sm:mt-16 sm:px-6 sm:py-16 lg:py-20 ">
        <div className="mx-auto max-w-7xl">

          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="grid items-center gap-10 lg:grid-cols-[454px_minmax(0,1fr)] lg:gap-12 xl:gap-16"
          >


            <div className="mx-auto aspect-[454/402] w-full max-w-[454px] overflow-hidden rounded-[24px]">
              <Image
                src="/icons/program/7.png"
                alt=""
                width={454}
                height={402}
                className="h-full w-full object-cover"
                sizes="(max-width: 1024px) 100vw, 454px"
                priority
              />
            </div>

            <div dir={isRtl ? "rtl" : "ltr"} className="space-y-8">
              <h3 className="text-start text-2xl font-bold text-secondary sm:text-3xl">
                {content.coursesTitle}
              </h3>
              <div className="grid w-full max-w-3xl grid-flow-col grid-rows-3 gap-x-10 sm:gap-x-14 lg:max-w-none lg:gap-x-16 xl:gap-x-20">
                {content.courses.map((course, index) => (
                  <CourseRow
                    key={`${course.title}-${index}`}
                    title={course.title}
                    count={course.count}
                    isRtl={isRtl}
                  />
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
