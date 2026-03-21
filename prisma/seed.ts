import prisma from '../lib/prisma';

async function main() {
  const user = await prisma.user.create({
    data: {
      email: 'admin@uk-ads.co.uk',
      name: 'UK Admin',
      password: 'securepassword',
    },
  });

  const ads = [
    {
      title: '2021 Land Rover Defender 110',
      description: 'Excellent condition, low mileage, full service history. Local pickup in London.',
      price: 55000,
      location: 'London',
      category: 'Cars & Vehicles',
      authorId: user.id,
      imageUrl: 'https://images.unsplash.com/photo-1613944321768-46d29944a14f?q=80&w=800&auto=format&fit=crop',
    },
    {
      title: 'iPhone 15 Pro Max 256GB - Blue Titanium',
      description: 'Brand new in box, unlocked to all UK networks. Warranty included.',
      price: 950,
      location: 'Manchester',
      category: 'Electronics',
      authorId: user.id,
      imageUrl: 'https://images.unsplash.com/photo-1696446701796-da61225697cc?q=80&w=800&auto=format&fit=crop',
    },
    {
      title: 'Golden Retriever Puppies',
      description: 'KC registered, microchipped and vaccinated. Available for their forever homes in Birmingham.',
      price: 1200,
      location: 'Birmingham',
      category: 'Pets',
      authorId: user.id,
      imageUrl: 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=800&auto=format&fit=crop',
    },
  ];

  for (const ad of ads) {
    await prisma.ad.create({ data: ad });
  }

  console.log('Seed completed successfully!');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
