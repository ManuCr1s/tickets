export function validate(size,input){
    return (size==input.length);
}
export function onlyNumbers(code){
    let variable = code.charCode;
    return variable >= 48 && variable <= 57;
}