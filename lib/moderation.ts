/**
 * Moderation Utility for UK Classifieds
 * Automatically flags inappropriate content or scams.
 */

export async function moderateContent(title: string, description: string): Promise<{
  safe: boolean;
  score: number;
  reason?: string;
}> {
  // Simple heuristic for demo purposes.
  // In production, this would call OpenAI Moderation API or a custom LLM endpoint.
  const blacklistedKeywords = ['scam', 'spam', 'illegal', 'hack', 'drugs', 'weapons'];
  const content = `${title} ${description}`.toLowerCase();

  const foundKeywords = blacklistedKeywords.filter(keyword => content.includes(keyword));

  if (foundKeywords.length > 0) {
    return {
      safe: false,
      score: 0.9,
      reason: `Found restricted keywords: ${foundKeywords.join(', ')}`
    };
  }

  // Simulate a more advanced check for potential scams (e.g., extremely low price for high-value items)
  if (title.toLowerCase().includes('iphone 15') && content.includes('£10')) {
    return {
      safe: false,
      score: 0.95,
      reason: 'Potential scam: Price suspiciously low for high-value tech.'
    };
  }

  return {
    safe: true,
    score: 0.01
  };
}
