import React, { useState } from 'react';
import {
  Box,
  Button,
  FormControl,
  FormLabel,
  Input,
  Textarea,
  Select,
  VStack,
  Accordion,
  AccordionItem,
  AccordionButton,
  AccordionPanel,
  AccordionIcon,
  CloseButton,
  FormErrorMessage,
  useToast,
  Text,
} from '@chakra-ui/react';
import axios from 'axios';

const CourseForm = () => {
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    modules: [],
  });
  const [errors, setErrors] = useState({});
  const toast = useToast();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleModuleChange = (index, value) => {
    const modules = [...formData.modules];
    modules[index].title = value;
    setFormData({ ...formData, modules });
  };

  const handleContentChange = (mIndex, cIndex, field, value) => {
    const modules = [...formData.modules];
    modules[mIndex].contents[cIndex][field] = value;
    setFormData({ ...formData, modules });
  };

  const addModule = () => {
    setFormData((prev) => ({
      ...prev,
      modules: [...prev.modules, { title: '', contents: [] }],
    }));
  };

  const removeModule = (index) => {
    const modules = [...formData.modules];
    modules.splice(index, 1);
    setFormData({ ...formData, modules });
  };

  const addContent = (moduleIndex) => {
    const modules = [...formData.modules];
    modules[moduleIndex].contents.push({ data: '', type: 'text' });
    setFormData({ ...formData, modules });
  };

  const removeContent = (mIndex, cIndex) => {
    const modules = [...formData.modules];
    modules[mIndex].contents.splice(cIndex, 1);
    setFormData({ ...formData, modules });
  };

  const handleSubmit = async () => {
    try {
      await axios.post('/courses', formData);
      toast({ title: 'Course created successfully', status: 'success', duration: 3000, isClosable: true });
      setFormData({ title: '', description: '', modules: [] });
      setErrors({});
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors);
        Object.values(err.response.data.errors).forEach((msg) => {
          toast({ title: msg[0], status: 'error', duration: 4000, isClosable: true });
        });
      } else {
        toast({ title: 'An unexpected error occurred.', status: 'error', duration: 3000, isClosable: true });
      }
    }
  };

  return (
    <Box maxW="800px" mx="auto" mt={5}>
      <VStack spacing={4} align="stretch">
        <FormControl isRequired isInvalid={errors.title}>
          <FormLabel>Title</FormLabel>
          <Input name="title" value={formData.title} onChange={handleChange} />
          <FormErrorMessage>{errors.title && errors.title[0]}</FormErrorMessage>
        </FormControl>

        <FormControl isRequired isInvalid={errors.description}>
          <FormLabel>Description</FormLabel>
          <Textarea name="description" value={formData.description} onChange={handleChange} />
          <FormErrorMessage>{errors.description && errors.description[0]}</FormErrorMessage>
        </FormControl>

        <Button onClick={addModule} colorScheme="blue">Add Module +</Button>

        <Accordion allowToggle>
          {formData.modules.map((module, mIndex) => (
            <AccordionItem key={mIndex}>
              <h2>
                <AccordionButton>
                  <Box flex="1" textAlign="left">Module {mIndex + 1}</Box>
                  <AccordionIcon />
                  <CloseButton size="sm" onClick={(e) => { e.stopPropagation(); removeModule(mIndex); }} />
                </AccordionButton>
              </h2>
              <AccordionPanel pb={4}>
                <FormControl isRequired isInvalid={errors[`modules.${mIndex}.title`]} mb={2}>
                  <FormLabel>Module Title</FormLabel>
                  <Input
                    value={module.title}
                    onChange={(e) => handleModuleChange(mIndex, e.target.value)}
                  />
                  <FormErrorMessage>{errors[`modules.${mIndex}.title`]}</FormErrorMessage>
                </FormControl>

                <Button size="sm" onClick={() => addContent(mIndex)} mb={3}>Add Content +</Button>

                {module.contents.map((content, cIndex) => (
                  <Box key={cIndex} p={3} border="1px solid #ccc" borderRadius="md" mb={2}>
                    <FormControl isRequired isInvalid={errors[`modules.${mIndex}.contents.${cIndex}.data`]}>
                      <FormLabel>Content Title</FormLabel>
                      <Input
                        value={content.data}
                        onChange={(e) => handleContentChange(mIndex, cIndex, 'data', e.target.value)}
                      />
                      <FormErrorMessage>{errors[`modules.${mIndex}.contents.${cIndex}.data`]}</FormErrorMessage>
                    </FormControl>
                    <FormControl mt={2}>
                      <FormLabel>Content Type</FormLabel>
                      <Select
                        value={content.type}
                        onChange={(e) => handleContentChange(mIndex, cIndex, 'type', e.target.value)}
                      >
                        <option value="text">Text</option>
                        <option value="video">Video</option>
                        <option value="image">Image</option>
                        <option value="link">Link</option>
                      </Select>
                    </FormControl>
                    <CloseButton size="sm" mt={2} onClick={() => removeContent(mIndex, cIndex)} />
                  </Box>
                ))}
              </AccordionPanel>
            </AccordionItem>
          ))}
        </Accordion>

        <Button
          size="md"
          height="48px"
          width="full"
          bg="green.500"
          color="white"
          _hover={{ bg: 'white', color: 'green.500' }}
          type="button"
          onClick={handleSubmit}
          w="50%"
        >
          Save
        </Button>
      </VStack>
    </Box>
  );
};

export default CourseForm;
