type Props = {
  text: string;
  isRtl: boolean;
};

export function FormatBox({ text, isRtl }: Props) {
  return (
    <div
      dir={isRtl ? "rtl" : "ltr"}
      className="flex items-center gap-4 rounded-2xl border border-primary/30 bg-[#eef4f8] px-5 py-4 sm:px-6 sm:py-5"
    >
      <span className="size-2.5 shrink-0 rounded-full bg-primary" aria-hidden />
      <p className="flex-1 text-start text-sm font-medium text-secondary sm:text-base">
        {text}
      </p>
    </div>
  );
}
