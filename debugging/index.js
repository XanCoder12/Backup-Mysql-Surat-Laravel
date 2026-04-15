console.log("Hello world");

let name = "Akrom";
let age = "17";
let isStudent = true; 

console.log("My name is", name, "and I am", age, "years old");
console.log("Am I a student?", isStudent);

if (isStudent) {
    console.log("I am a student");
} else {
    console.log("I am not a student");
}

for (let i = 0; i < 5; i++) {
    console.log(i);
}

function greet(name) {
    console.log("Hello", name);
}

greet("Akrom");

const numbers = [1, 2, 3, 4, 5];

for (let i = 0; i < numbers.length; i++) {
    console.log(numbers[i]);
}

for (let i = 0; i < numbers.length; i++) {
    console.log("The number is", numbers[i]);
}