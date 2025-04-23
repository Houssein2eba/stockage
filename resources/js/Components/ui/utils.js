import { clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs) {
  return twMerge(clsx(inputs))
}

// This function is used to create a context that can be used to pass data down the component tree
export function createContext(rootComponentName) {
  const symVal = Symbol(rootComponentName)

  return {
    Provider: {
      props: {
        value: {
          type: null,
          required: true,
        },
      },
      setup(props, { slots }) {
        provide(symVal, toRef(props, 'value'))
        return () => slots.default?.()
      },
    },
    Consumer: {
      setup(_, { slots }) {
        const value = inject(symVal)
        return () => slots.default?.(value?.value)
      },
    },
    useContext() {
      const value = inject(symVal)
      if (!value) {
        throw new Error(
          `\`${rootComponentName}\` is missing a parent <${rootComponentName}.Provider> component.`
        )
      }
      return value
    },
  }
}
