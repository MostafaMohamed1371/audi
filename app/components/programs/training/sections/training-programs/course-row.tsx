function CourseCountBadge({ count }: { count: string }) {
  return (
    <span className="shrink-0 whitespace-nowrap rounded-full bg-primary px-4 py-2 text-right align-middle text-base font-normal leading-none tracking-[-0.45px] text-white  ">
      {count}
    </span>
  );
}

type Props = {
  title: string;
  count: string;
  isRtl: boolean;
};

export function CourseRow({ title, count, isRtl }: Props) {
  return (
    <div
      dir={isRtl ? "rtl" : "ltr"}
      className="flex items-center gap-3 border-b border-[#c5d4de] py-5 sm:py-6 "
    >
      <span className="size-2 shrink-0 rounded-full bg-primary" aria-hidden />
      <p className="min-w-0 flex-1 text-right align-middle text-[15px] font-normal leading-none tracking-[-0.45px] text-secondary">
        {title}
      </p>
      <CourseCountBadge count={count} />
    </div>
  );
}
