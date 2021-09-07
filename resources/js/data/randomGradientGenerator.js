
const random = (part) => {
let rand = part.group === 'color' ?  Math.floor((1 + Math.random()) * 256/2) :
Math.floor(Math.random() * (part.max - part.min) + part.min);

  if (part.type === 'deg' && rand % 5 !== 0) {
     while(rand % 5 !== 0) {
       rand = rand + 1;
     }
  }
  if (part.type === 'alpha') {
     rand = parseFloat(`0.${rand}`);
  }
  part.value = rand;
}

 const generateRGBA = (count) => {

  const parts  = [
   {type: 'red', min: 150 ,max: 255, value: null, group: 'color'},
   {type: 'green', min: 100, max: 255, value: null, group: 'color'},
   {type: 'blue', min: 50, max: 255, value: null, group: 'color'},
   {type: 'alpha', min: 7, max: 9, value: null, group: 'enhancer'},
];
   parts.forEach(part => random(part));
   return parts.map(part => part.value).join(', ');
}

const constructRGBA = (count) => {
  const values = [];
  for (let i = 0; i < count; i++) {
     values.push(`rgba(${generateRGBA()})`);
  }
  return values;
}

const generateGradient = () => {
  const deg = {type: 'deg', min: 5, max: 360,  value: null, group: 'enhancer'};
  random(deg);

  return `linear-gradient(${deg.value}deg, ${constructRGBA(2)})`;
};

export const randomGradient = () => {
  let randomGradient = '';

  for (let i = 0; i < 4; i++) {
    let separator = i + 1 === 4 ? ';' : ', ';
    randomGradient += `${generateGradient()}${separator}`;
  }
  return randomGradient;
}
