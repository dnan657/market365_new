import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  await prisma.adAttribute.deleteMany();
  await prisma.categoryField.deleteMany();
  await prisma.subcategory.deleteMany();
  await prisma.category.deleteMany();
  await prisma.adImage.deleteMany();
  await prisma.ad.deleteMany();
  await prisma.user.deleteMany();

  const user = await prisma.user.create({
    data: { email: 'admin@uk-ads.co.uk', name: 'UK Admin', password: 'securepassword', role: 'SUPERADMIN' },
  });

  // Create Categories
  const cars = await prisma.category.create({ data: { name: 'Cars & Vehicles', slug: 'cars' } });
  const property = await prisma.category.create({ data: { name: 'Property', slug: 'property' } });

  // Subcategories & Fields
  const usedCars = await prisma.subcategory.create({
    data: {
      name: 'Used Cars',
      slug: 'used-cars',
      categoryId: cars.id,
      fields: {
        create: [
          { name: 'Make', type: 'SELECT', options: JSON.stringify(['Ford', 'Volkswagen', 'BMW', 'Audi', 'Tesla']), required: true },
          { name: 'Model', type: 'TEXT', required: true },
          { name: 'Mileage', type: 'NUMBER', required: true },
          { name: 'Fuel Type', type: 'SELECT', options: JSON.stringify(['Petrol', 'Diesel', 'Electric', 'Hybrid']) }
        ]
      }
    }
  });

  const apartments = await prisma.subcategory.create({
    data: {
      name: 'Apartments for Rent',
      slug: 'rent-apartments',
      categoryId: property.id,
      fields: {
        create: [
          { name: 'Bedrooms', type: 'NUMBER', required: true },
          { name: 'Furnished', type: 'SELECT', options: JSON.stringify(['Yes', 'No', 'Part-furnished']) }
        ]
      }
    }
  });

  // Sample Ad with Attributes
  const carFieldMake = await prisma.categoryField.findFirst({ where: { name: 'Make', subcategoryId: usedCars.id } });
  const carFieldMileage = await prisma.categoryField.findFirst({ where: { name: 'Mileage', subcategoryId: usedCars.id } });

  await prisma.ad.create({
    data: {
      title: '2021 Land Rover Defender 110',
      description: 'Excellent condition, low mileage, full service history. Local pickup in London.',
      price: 55000,
      location: 'London',
      categoryName: 'Cars & Vehicles',
      subcategoryId: usedCars.id,
      authorId: user.id,
      status: 'APPROVED',
      images: { create: [{ url: 'https://images.unsplash.com/photo-1613944321768-46d29944a14f?q=80&w=800&auto=format&fit=crop' }] },
      attributes: {
        create: [
          { fieldId: carFieldMake!.id, value: 'Land Rover' },
          { fieldId: carFieldMileage!.id, value: '12000' }
        ]
      }
    }
  });

  console.log('Advanced Seed completed!');
}

main().catch(console.error).finally(() => prisma.$disconnect());
