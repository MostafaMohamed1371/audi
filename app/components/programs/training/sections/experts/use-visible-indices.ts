export function useVisibleIndices(current: number, total: number) {
  const indices: number[] = [];
  for (let i = -1; i <= 1; i++) {
    const idx = (current + i + total) % total;
    indices.push(idx);
  }
  return indices;
}
