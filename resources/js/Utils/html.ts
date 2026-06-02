/**
 * Supprime les balises HTML et retourne le texte brut tronqué
 */
export const stripHtml = (html: string, maxLength?: number): string => {
    const text = (html ?? '').replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
    if (maxLength && text.length > maxLength) return text.slice(0, maxLength) + '…';
    return text;
};
