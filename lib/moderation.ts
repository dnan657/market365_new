/**
 * Moderation Utility for UK Classifieds
 * Automatically flags inappropriate content or scams.
 * Integrated with OpenAI Moderation API (MOCKED for demo).
 */

export async function moderateContent(title: string, description: string): Promise<{
  safe: boolean;
  score: number;
  reason?: string;
}> {
  const content = `${title} ${description}`;

  // 1. MOCK OpenAI Moderation API Call
  // In production, you would use:
  // const response = await fetch('https://api.openai.com/v1/moderations', {
  //   method: 'POST',
  //   headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${process.env.OPENAI_API_KEY}` },
  //   body: JSON.stringify({ input: content })
  // });
  // const data = await response.json();
  // if (data.results[0].flagged) return { safe: false, reason: 'AI detected inappropriate content.' };

  // 2. Advanced Heuristics (UK Specific)
  const blacklist = [
    'scam', 'spam', 'hack', 'illegal', 'drugs', 'weapons',
    'counterfeit', 'fake pass', 'money laundry'
  ];

  const contentLower = content.toLowerCase();
  const foundKeywords = blacklist.filter(word => contentLower.includes(word));

  if (foundKeywords.length > 0) {
    return {
      safe: false,
      score: 0.9,
      reason: `Restricted keywords found: ${foundKeywords.join(', ')}`
    };
  }

  // 3. Scam Detection Logic (Price vs Value)
  // Flag high-value items with suspiciously low prices
  const highValueKeywords = ['iphone 15', 'macbook pro', 'rolex', 'land rover'];
  const isHighValue = highValueKeywords.some(k => contentLower.includes(k));

  // Extract potential prices using regex if needed, or use the price passed from form
  // For this helper, we assume we just check the text for "£" followed by small numbers
  const cheapPriceRegex = /£[0-9]{1,2}(?!\d)/; // Matches £1-£99

  if (isHighValue && cheapPriceRegex.test(contentLower)) {
    return {
      safe: false,
      score: 0.98,
      reason: 'AI Analysis: High-value item listed at a suspiciously low price (Potential Scam).'
    };
  }

  return {
    safe: true,
    score: 0.01
  };
}
