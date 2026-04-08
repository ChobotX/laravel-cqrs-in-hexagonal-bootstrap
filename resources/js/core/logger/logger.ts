export function debug(message: string, ...context: unknown[]): void {
    console.debug(message, ...context);
}

export function info(message: string, ...context: unknown[]): void {
    console.info(message, ...context);
}

export function warn(message: string, ...context: unknown[]): void {
    console.warn(message, ...context);
}

export function error(message: string, ...context: unknown[]): void {
    console.error(message, ...context);
}
