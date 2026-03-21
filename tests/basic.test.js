const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function runTest() {
  console.log('--- RUNNING BASIC SYSTEM TEST ---');
  try {
    const userCount = await prisma.user.count();
    console.log(`Database Connected. User Count: ${userCount}`);
    process.exit(0);
  } catch (err) {
    console.error('Verification Failure:', err);
    process.exit(1);
  }
}

runTest();
