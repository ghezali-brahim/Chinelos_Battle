import { Element } from '../types/models'

const ELEMENT_ICON_PATH = '/img/element/'

export const getElementIcon = (id_element: number): string => {
  return `${ELEMENT_ICON_PATH}${id_element}.png`
}

export const getElementName = (elements: Element[], id_element: number): string => {
  const element = elements.find(e => e.id_element === id_element)
  return element?.nom || 'Inconnu'
}

export const getIdElementsFortContre = (element: Element): number[] => {
  if (!element.id_fort_contre) return []
  return element.id_fort_contre.split(';').map(id => parseInt(id)).filter(id => !isNaN(id))
}

export const getIdElementsFaibleContre = (element: Element): number[] => {
  if (!element.id_faible_contre) return []
  return element.id_faible_contre.split(';').map(id => parseInt(id)).filter(id => !isNaN(id))
}

export const getRatioDegatElement = (id_element: number, id_element_target: number, elements: Element[]): number => {
  const element = elements.find(e => e.id_element === id_element)
  if (!element) return 1.0

  const fortsContre = getIdElementsFortContre(element)
  const faiblesContre = getIdElementsFaibleContre(element)

  if (fortsContre.includes(id_element_target)) {
    return 1.5
  } else if (faiblesContre.includes(id_element_target)) {
    return 0.5
  }
  return 1.0
}

