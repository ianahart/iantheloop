export const goToYears = () => {
  const yearsInPast = 14;
  const date = new Date();
  let iteration = date.getFullYear();
  const years = [];

  for (let i = 0; i < yearsInPast; i++) {
    const year = { name: iteration.toString(), abbrv: iteration.toString(), id: i };

      if (i === 0) {
        continue;
      }

      iteration--;
    years.push(year);
  }
  return years;
}

