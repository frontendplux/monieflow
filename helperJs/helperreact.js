let hooks = [];
let currentHook = 0;
let rootComponent = null;

// 🔁 Re-render function
function render(component) {
    currentHook = 0;
    rootComponent = component;
    document.getElementById("app").innerHTML = component();
}

// 🧠 useState
function useState(initialValue) {
    const hookIndex = currentHook;

    hooks[hookIndex] = hooks[hookIndex] ?? initialValue;

    const setState = (newValue) => {
        hooks[hookIndex] =
            typeof newValue === "function"
                ? newValue(hooks[hookIndex])
                : newValue;

        render(rootComponent); // 🔁 trigger re-render
    };

    currentHook++;
    return [hooks[hookIndex], setState];
}

// ⚡ useEffect
function useEffect(callback, deps) {
    const hookIndex = currentHook;
    const oldDeps = hooks[hookIndex];

    let hasChanged = true;

    if (oldDeps) {
        hasChanged = deps
            ? !deps.every((dep, i) => dep === oldDeps[i])
            : true;
    }

    if (hasChanged) {
        setTimeout(callback, 0); // async like React
        hooks[hookIndex] = deps;
    }

    currentHook++;
}