type TaskItem = {
  description: string;
};

type Props = {
  title: string;
  items: TaskItem[];
  showTitleAccent?: boolean;
};

export function InstituteTasksSection({
  title,
  items,
  showTitleAccent = false,
}: Props) {
  return (
    <section className="bg-[#f4f7f9] py-16 sm:py-20 lg:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <h2 className="mb-12 flex items-center justify-center gap-3 text-2xl font-bold text-secondary sm:mb-14 sm:text-3xl lg:text-4xl">
          {showTitleAccent && (
            <span className="size-2.5 shrink-0 rounded-sm bg-primary" aria-hidden />
          )}
          {title}
        </h2>

        <div className="grid gap-6 pt-4 sm:grid-cols-2 lg:grid-cols-5 lg:gap-5 xl:gap-6">
          {items.map((item, index) => {
            const number = String(index + 1).padStart(2, "0");

            return (
              <article
                key={number}
                className="group relative flex min-h-[220px] flex-col items-center rounded-2xl bg-white px-4 pb-8 pt-12 text-center shadow-[0_8px_32px_rgba(17,31,66,0.06)] transition-colors duration-300 hover:bg-primary sm:min-h-[240px] sm:px-5 lg:min-h-[260px]"
              >
                <span className="absolute -top-5 flex size-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white transition-colors duration-300  border border-3 border-white">
                  {number}
                </span>

                <p className="text-sm leading-7 text-secondary transition-colors duration-300 group-hover:text-white sm:text-[0.95rem] sm:leading-8">
                  {item.description}
                </p>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}
