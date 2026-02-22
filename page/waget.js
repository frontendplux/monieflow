 export default function timeAgo(timestamp) {
  const seconds = Math.floor((Date.now() - new Date(timestamp).getTime()) / 1000);

  const units = [
    { label: 'yr', value: 60 * 60 * 24 * 365 },
    { label: 'mo', value: 60 * 60 * 24 * 30 },
    { label: 'd',  value: 60 * 60 * 24 },
    { label: 'h',  value: 60 * 60 },
    { label: 'm',  value: 60 },
    { label: 's',  value: 1 }
  ];

  for (let unit of units) {
    if (seconds >= unit.value) {
      const count = Math.floor(seconds / unit.value);
      return `${count}${unit.label}`;
    }
  }
  return '0s';
}
